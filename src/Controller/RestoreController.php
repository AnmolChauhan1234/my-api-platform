<?php

// src/Controller/RestoreController.php

// src/Controller/RestoreController.php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RestoreController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/api/restore', name: 'api_restore', methods: ['POST'])]
    public function restoreDatabase(Request $request): Response
    {
        // Ensure the file is uploaded and valid
        $file = $request->files->get('file');
        if (!$file || !$file->isValid() || $file->getClientOriginalExtension() !== 'sql') {
            return new Response('Invalid backup file', Response::HTTP_BAD_REQUEST);
        }

        // Save the uploaded file to a temporary location
        $tempPath = sys_get_temp_dir() . '/' . uniqid('backup_', true) . '.sql';
        $file->move(sys_get_temp_dir(), basename($tempPath));

        // Database connection details
        $dbHost = $this->getParameter('database_host');
        $dbPort = $this->getParameter('database_port');
        $dbUser = $this->getParameter('database_user');
        $dbName = $this->getParameter('database_name');
        $dbPass = $this->getParameter('database_password');

        // Construct the command for restoring the database using `mysql`
        $command = [
            'mysql',
            '-h', $dbHost,
            '-P', $dbPort,
            '-u', $dbUser,
            '-p' . $dbPass,
            $dbName
        ];

        // Set up the process to execute the command, passing the file as input
        $process = new Process($command);
        $process->setInput(file_get_contents($tempPath)); // Provide the file contents to mysql
        $process->setTimeout(300); // Set timeout to 5 minutes (300 seconds)

        try {
            $process->mustRun();
            // Clean up the temporary file
            unlink($tempPath);

            return new Response('Database restored successfully', Response::HTTP_OK);
        } catch (ProcessFailedException $exception) {
            // Log the exception details
            $this->logger->error('Restore process failed: ' . $exception->getMessage());
            return new Response(
                'Failed to restore database: ' . $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
