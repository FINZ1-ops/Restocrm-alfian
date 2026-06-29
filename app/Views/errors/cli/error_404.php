<?php
/**
 * @var mixed $code
 * @var mixed $message
 */
use CodeIgniter\CLI\CLI;

CLI::error('ERROR: ' . $code);
CLI::write($message);
CLI::newLine();
