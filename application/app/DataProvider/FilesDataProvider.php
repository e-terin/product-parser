<?php

namespace App\DataProvider;

use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Writer;

class FilesDataProvider
{
	const CHANK = 7000;

	/**
	 * @var string Что делаем
	 */
	private string $dir;

    public function __construct($dir)
    {
        if (!is_dir(strtolower($dir))) {
            throw new \InvalidArgumentException('Directory ' . $dir . ' not exist!');
        }
        $this->dir = $dir;
    }

	/**
	 * @return mixed
	 */
	public function getCsvFiles():array
	{
		return glob($this->dir. '*.csv') ?? [];
	}

	/**
	 * Читает csv-файл и возвращает массив содержащий заголовок и строки
	 *
	 * @param string $filename
	 * @return array
	 */
	public function getCsvFile(string $filename): array
	{

		try {

			$csv = Reader::createFromPath($filename, 'r');
			$csv->setHeaderOffset(0);
			$csv->setDelimiter(';');
			$csv->setOutputBOM(Reader::BOM_UTF8);
			//let's convert the incoming data from iso-88959-15 to utf-8
			$csv->addStreamFilter('convert.iconv.WINDOWS-1251/UTF-8');

			$header = $csv->getHeader(); //returns the CSV header record
			$records = $csv->getRecords(); //returns all the CSV records as an Iterator object

		} catch (Exception $e) {
			echo 'Не удалось прочитать csv-файл ' . $filename;
			exit;
		}

		return [
			'header'  => $header,
			'records' => $records,
		];
	}

	/**
	 * @param $new_files array имеет специальный формат
	 * @throws \League\Csv\CannotInsertRecord
	 * @throws \League\Csv\InvalidArgument
	 */
	public function saveFileToCsvByMarka($new_files, $delaem)
	{
		$counter_file = 0;
		foreach ($new_files as $marka=>$data_for_file)
		{

			// разбиваем марки на чанки по 9000 строк
			$kolvo_mod = $data_for_file['mod_qty'] ?? 1;

			$kolvo_tovarov = floor(self::CHANK/$kolvo_mod);
			$kolvo_strok_in_chank = $kolvo_tovarov * $kolvo_mod;

			$data = array_chunk($data_for_file['data'], $kolvo_strok_in_chank);

			$dir = $_ENV['DIR_DOWNLOAD'] . $delaem . '/' . $marka . '/';

			if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir))
			{
				throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
			}


			foreach ($data as $i=>$file)
			{
				$index = $i ? '-' . $i : '';
				$filename =  $dir. $marka. $index . '.csv';
				$writer = Writer::createFromPath($filename, 'w+');
				$writer->setDelimiter(';');
				$writer->insertOne($data_for_file['header']);
				$writer->insertAll($file); //using an array
				//$writer->insertAll(new ArrayIterator($records)); //using a Traversable object
				//echo $csv->getContent(); //returns the CSV document as a string
				echo('Сбросил все в файл ' . $filename.PHP_EOL);
				$counter_file++;
			}

			// проверка на соответствие заголовка и строчки
		}
	}


	public function saveFileToCsv($new_file, $delaem, $subfolder = 'RAL')
	{
		$counter_file = 0;

		// разбиваем марки на чанки по 9000 строк
		//$kolvo_mod = $data_for_file['mod_qty'] ?? 1;

		//$kolvo_tovarov = floor(self::CHANK/$kolvo_mod);
		//$kolvo_strok_in_chank = $kolvo_tovarov * $kolvo_mod;

		//$data = array_chunk($data_for_file['data'], $kolvo_strok_in_chank);

		$dir = $_ENV['DIR_DOWNLOAD'] . $delaem . '/' . $subfolder. '/';
		if ( !mkdir($dir, 0777, true) && !is_dir($dir)) {
			throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
		}

		$filename =  $dir. 'out.csv';
		$writer = Writer::createFromPath($filename, 'w+');
		$writer->setDelimiter(';');
		$writer->insertOne($new_file['header']);
		$writer->insertAll($new_file['data']); //using an array
		//$writer->insertAll(new ArrayIterator($records)); //using a Traversable object
		//echo $csv->getContent(); //returns the CSV document as a string
		echo('Сбросил все в файл ' . $filename.PHP_EOL);
		$counter_file++;
		// проверка на соответствие заголовка и строчки
	}

	/**
	 * @param array $array
	 * @param string $filename
	 * @return bool
	 * @throws \League\Csv\InvalidArgument
	 */
	public function saveArrayToCsv(array $array, string $filename = 'out.csv'): bool
	{
		if (empty($array)){
			return false;
		}

		$dir = $_ENV['DIR_DOWNLOAD'].'/' . $this->delaem.'/';
		self::checkDir($dir);

		$file =  $dir. $filename;
		$writer = Writer::createFromPath($file, 'w+');
		$writer->setDelimiter(';');
		$writer->insertAll($array); //using an array

		return self::checkNotEmptyFile($file);
	}

	/**
	 * Проверяет существование директории и создает ее при ее отсутствии
	 * @param string $dir
	 */
	public static function checkDir(string $dir): void
	{
		if ( !is_dir( $dir ) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
			throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
		}
	}

	public static function checkNotEmptyFile(string $filename): bool
	{
		return file_exists($filename) && filesize($filename) > 0;
	}

}