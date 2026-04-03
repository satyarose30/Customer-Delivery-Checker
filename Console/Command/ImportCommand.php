<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Console\Command;

use Domus\CustomerDeliveryChecker\Model\PincodeFactory;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode as PincodeResource;
use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    public function __construct(
        private readonly PincodeFactory $pincodeFactory,
        private readonly PincodeResource $pincodeResource,
        private readonly CollectionFactory $collectionFactory
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('domus:pincode:import')
            ->setDescription('Import pincodes from a CSV file')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the CSV file')
            ->addOption('update', 'u', InputOption::VALUE_NONE, 'Update existing records')
            ->addOption('delimiter', 'd', InputOption::VALUE_OPTIONAL, 'CSV delimiter', ',')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run without saving');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        $updateExisting = (bool)$input->getOption('update');
        $delimiter = $input->getOption('delimiter');
        $dryRun = (bool)$input->getOption('dry-run');

        if (!file_exists($filePath)) {
            $output->writeln('<error>File not found: ' . $filePath . '</error>');
            return Command::FAILURE;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $output->writeln('<error>Unable to open file: ' . $filePath . '</error>');
            return Command::FAILURE;
        }

        $headers = fgetcsv($handle, 0, $delimiter);
        $headers = array_map(fn(string $h) => strtolower(trim($h)), $headers);

        $requiredColumns = ['pincode', 'country_id'];
        foreach ($requiredColumns as $required) {
            if (!in_array($required, $headers)) {
                $output->writeln('<error>Missing required column: ' . $required . '</error>');
                fclose($handle);
                return Command::FAILURE;
            }
        }

        $imported = 0;
        $skipped = 0;
        $updated = 0;
        $lineNumber = 1;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $lineNumber++;
            $data = array_combine($headers, $row);

            if (empty($data['pincode']) || empty($data['country_id'])) {
                $output->writeln('<comment>Skipping line ' . $lineNumber . ': missing pincode or country_id</comment>');
                $skipped++;
                continue;
            }

            $pincode = trim($data['pincode']);
            $countryId = trim($data['country_id']);

            $collection = $this->collectionFactory->create();
            $collection->addPincodeFilter($pincode)
                ->addCountryFilter($countryId);
            $existing = $collection->getFirstItem();

            if ($existing->getEntityId()) {
                if (!$updateExisting) {
                    $output->writeln('<comment>Skipping line ' . $lineNumber . ': pincode ' . $pincode . ' already exists</comment>');
                    $skipped++;
                    continue;
                }

                if (!$dryRun) {
                    $this->updateFromData($existing, $data);
                    $this->pincodeResource->save($existing);
                }
                $updated++;
                $output->writeln('<info>Updated: ' . $pincode . ' (' . $countryId . ')</info>');
            } else {
                if (!$dryRun) {
                    $model = $this->pincodeFactory->create();
                    $this->updateFromData($model, $data);
                    $model->setPincode($pincode);
                    $model->setCountryId($countryId);
                    $this->pincodeResource->save($model);
                }
                $imported++;
                $output->writeln('<info>Imported: ' . $pincode . ' (' . $countryId . ')</info>');
            }
        }

        fclose($handle);

        $output->writeln('');
        $output->writeln('<info>Import completed!</info>');
        $output->writeln('  Imported: ' . $imported);
        $output->writeln('  Updated: ' . $updated);
        $output->writeln('  Skipped: ' . $skipped);

        if ($dryRun) {
            $output->writeln('<comment>Dry run mode - no data was saved.</comment>');
        }

        return Command::SUCCESS;
    }

    private function updateFromData($model, array $data): void
    {
        $fieldMap = [
            'city' => 'setCity',
            'state' => 'setState',
            'area_name' => 'setAreaName',
            'latitude' => 'setLatitude',
            'longitude' => 'setLongitude',
            'is_deliverable' => 'setIsDeliverable',
            'is_cod_available' => 'setIsCodAvailable',
            'estimated_delivery_days' => 'setEstimatedDeliveryDays',
            'shipping_charge' => 'setShippingCharge',
            'cod_charge' => 'setCodCharge',
            'weight_from' => 'setWeightFrom',
            'weight_to' => 'setWeightTo',
            'price_from' => 'setPriceFrom',
            'price_to' => 'setPriceTo',
            'store_id' => 'setStoreId',
            'customer_group_id' => 'setCustomerGroupId',
            'priority' => 'setPriority',
            'is_active' => 'setIsActive',
            'categories' => 'setCategories',
            'products' => 'setProducts',
        ];

        foreach ($fieldMap as $column => $setter) {
            if (isset($data[$column]) && $data[$column] !== '') {
                $value = $data[$column];
                if (str_contains($setter, 'Is')) {
                    $value = (bool)$value;
                } elseif (str_contains($setter, 'Days') || str_contains($setter, 'Priority')) {
                    $value = (int)$value;
                } elseif (str_contains($setter, 'Charge') 
                          || str_contains($setter, 'Weight') 
                          || str_contains($setter, 'Price')
                          || str_contains($setter, 'From')
                          || str_contains($setter, 'To')) {
                    $value = (float)$value;
                } elseif ($setter === 'setCategories' || $setter === 'setProducts') {
                    $value = array_filter(array_map('intval', explode(',', $value)));
                }
                $model->$setter($value);
            }
        }
    }
}
