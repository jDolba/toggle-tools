<?php

declare(strict_types=1);

namespace App;

use League\Csv\Reader;
use League\Csv\Writer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ToggleCsvToDailyReportCommand extends Command
{

    private const COL_START_DATE = 'Start date';
    private const COL_START_TIME = 'Start time';
    private const COL_END_TIME = 'End time';
    private const COL_DURATION = 'Duration';

    public function configure(): void
    {
        $this->setName('toggle-tools:csv2daily');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {

        $finder = (new Finder())
            ->files()
            ->in(__DIR__ . '/../data')
            ->name('~\.csv$~')
            ->ignoreDotFiles(true);

        if (!$finder->hasResults()) {
            $output->writeln('No Files in Data directory provided');
            return 0;
        }

        foreach ($finder as $file) {
            $output->writeln(sprintf('File: %s', $file->getFilename()));

            $csvReader = Reader::createFromPath($file->getRealPath());
            $csvReader->setHeaderOffset(0);
            $dayTimeSheets = $this->loadTimesheet($csvReader);

            $writer = Writer::createFromPath($file->getRealPath() . '.out', 'w+');
            $this->writeTimesheet($dayTimeSheets, $writer);

            $output->writeln('...done.');
        }

        return 0;
    }

    /**
     * @param Reader $csvReader
     * @return DayTimesheet[]
     */
    public function loadTimesheet(Reader $csvReader): array
    {
        $data = [];
        /** @var $data DayTimesheet[] */

        foreach ($csvReader->getRecords() as $record) {
            $startDate = $record[self::COL_START_DATE];

            //assuming records are sorted chronologically from youngest to oldest
            if (!array_key_exists($startDate, $data)) {
                $data[$startDate] = new DayTimesheet($record[self::COL_START_DATE], $record[self::COL_START_TIME]);
            }

            $data[$startDate]->addEndWithDuration($record[self::COL_END_TIME], $record[self::COL_DURATION]);
        }

        return $data;
    }

    /**
     * @param DayTimesheet[] $dayTimeSheets
     * @param Writer $writer
     */
    public function writeTimesheet(array $dayTimeSheets, Writer $writer): void
    {
        $writer->insertOne(['Den měsíce', 'Příchod', 'Přestávka', 'Odchod', 'Celkem',]);

        $timeFormat = 'H:i:s';
        $intervalFormat = '%H:%I:%S';
        $lastDayOfSheetInReport = 1;
        foreach ($dayTimeSheets as $dayTimeSheet) {
            $currentDayOfReport = (int)$dayTimeSheet->getStartTime()->format('j');

            for ($extraDays = 1; $extraDays < ($currentDayOfReport - $lastDayOfSheetInReport); $extraDays++) {
                // to fill weekends and days I haven't worked at all
                $writer->insertOne(
                    [$lastDayOfSheetInReport + $extraDays, '', '', '', '']
                );
            }

            $writer->insertOne(
                [
                    $currentDayOfReport,
                    $dayTimeSheet->getStartTime()->format($timeFormat),
                    $dayTimeSheet->getTotalBreaksFromStartToEnd()->format($intervalFormat),
                    $dayTimeSheet->getEndTime()->format($timeFormat),
                    $dayTimeSheet->getSpentWorkingTimeToday()->format($intervalFormat)
                ]
            );
            $lastDayOfSheetInReport = $currentDayOfReport;
        }
    }
}