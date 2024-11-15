<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/api/backup', name: 'api_backup', methods: ['GET'])]
    public function backupDatabase(): Response
    {
        // Get database connection details from parameters
        $dbHost = $this->getParameter('database_host');
        $dbPort = $this->getParameter('database_port');
        $dbUser = $this->getParameter('database_user');
        $dbPass = $this->getParameter('database_password');  // Corrected: database password
        $dbName = $this->getParameter('database_name');  // Corrected: database name

        // Backup file path (ensure this directory exists and is writable)
        $backupDir = __DIR__ . '/../../db_backup';
        $backupFile = $backupDir . '/backup.sql';

        // Check if the backup directory exists
        if (!file_exists($backupDir)) {
            // Create the directory if it doesn't exist
            mkdir($backupDir, 0777, true);  // 0777 ensures full permissions for all users

            // Optionally, log that the folder was created
            $this->logger->info('Backup directory created: ' . $backupDir);
        }

        // Ensure the directory is writable
        if (!is_writable($backupDir)) {
            return new Response(
                'The backup directory is not writable. Please check the permissions.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // Construct the command for mysqldump
        $command = [
            'mysqldump',
            '-h', $dbHost,
            '-P', $dbPort,
            '-u', $dbUser,
            '--password=' . $dbPass,  // Use --password for security
            $dbName
        ];

        // Set up the process to execute the command
        $process = new Process($command);
        $process->setTimeout(300); // Set timeout to 5 minutes (300 seconds)

        // Execute the command and capture output
        try {
            $process->mustRun();
            $output = $process->getOutput();

            // Write the output to the backup file
            file_put_contents($backupFile, $output);

            return new Response('Database backup created successfully', Response::HTTP_OK);
        } catch (ProcessFailedException $exception) {
            // Log the exception details
            $this->logger->error('Backup process failed: ' . $exception->getMessage());
            return new Response(
                'Failed to create database backup: ' . $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
