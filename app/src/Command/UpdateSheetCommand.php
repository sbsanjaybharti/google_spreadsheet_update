<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Service\GoogleConnection;


class UpdateSheetCommand extends Command
{
    protected static $defaultName = 'app:update-sheet';

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct($projectDir, $google_sheet_json, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->projectDir = $projectDir;
        $this->google_sheet_json = $google_sheet_json;
        parent::__construct();
    }

    protected function configure()
    {

        $this
            ->setDescription('Update google sheet')
            ->addArgument('file', InputArgument::REQUIRED, 'File path')
            ->addArgument('spread_sheet_id', InputArgument::REQUIRED, 'ID of sheet required')
            ->addArgument('range', InputArgument::OPTIONAL, 'sheet range or sheet name', 'sheet1')
            ->setHelp('This Command allows you to create a user...');
    }
    public function xml_to_array($file_path, $output){
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $decodeFile = new Serializer($normalizers, $encoders);
        try{
            $rows = $decodeFile->decode(file_get_contents($file_path), 'xml');
            return $rows['item'];
        }catch(\Exception $e){
            $this->logger->error('Invalid file or file not found:'.$file_path);
            throw new $e('ERROR: Invalid file or file not found.');
        }

    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $spreadsheetId = $input->getArgument('spread_sheet_id');
        $range = $input->getArgument('range');

        try {
            $data = $this->xml_to_array($input->getArgument('file'), $output);
            $service = new GoogleConnection($this->google_sheet_json, $spreadsheetId);

            $response = $service->get($range);
            if (empty($response->getValues())) {
                $values = array_keys($data[0]);
                unset($data[0]);
                $service->insert_row($values, $range);
            }
            foreach ($data as $row) {
                $values = array_values($row);
                $service->insert_row($values, $range);
            }
            // outputs a message followed by a "\n"
            $output->writeln('Google sheet updated with new records');
        }catch(\Exception $e){
            $this->logger->critical($e->getMessage());
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

}