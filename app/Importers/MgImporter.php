<?php


namespace App\Importers;


use App\Repositories\IFileRepository;

class MgImporter extends AbstractImporter {

    protected $options = [
        'delimiter' => ';',
        'enclosure' => '"',
        'escape' => '\\',
        'newline' => "\r\n",
        'input_encoding' => 'CP1250',
    ];

    protected $mapping = [
        'RokAkv' => 'acquisition_date',
        'DatExp' => 'copyright_expires',
        'Datace' => 'dating:cs',
        'RokOd' => 'date_earliest',
        'Do' => 'date_latest',
        'MístoVz' => 'place:cs',
        'Sign' => 'inscription:cs',
        'Původnost' => 'state_edition:cs',
        'Autor' => 'author',
        'Titul' => 'title:cs',
        'Námět' => 'topic:cs',
    ];

    protected $defaults = [
        'author' => 'neurčený autor',
        'gallery:cs' => 'Moravská galerie, MG',
        'title:cs' => 'bez názvu',
        'topic:cs' => '',
        'relationship_type:cs' => '',
        'description:cs' => '',
        'work_level:cs' => '',
        'subject:cs' => '',
        'item_type' => '',
        'featured' => 0,
    ];

    protected static $cz_work_types_spec = [
        'Ar' => 'architektura',
        'Bi' => 'bibliofilie a staré tisky',
        'Dř' => 'dřevo, nábytek a design',
        'Fo' => 'fotografie',
        'Gr' => 'grafika',
        'Gu' => 'grafický design',
        'Ji' => 'jiné',
        'Ke' => 'keramika',
        'Ko' => 'kovy',
        'Kr' => 'kresba',
        'Ob' => 'obrazy',
        'Sk' => 'sklo',
        'So' => 'sochy',
        'Šp' => 'šperky',
        'Te' => 'textil',
    ];

    protected static $cz_measurement_replacements = [
        'a' => 'výška hlavní části',
        'a.' => 'výška hlavní části',
        'b' => 'výška vedlejší části',
        'b.' => 'výška vedlejší části',
        'čas' => 'čas',
        'd' => 'délka',
        'd.' => 'délka',
        'h' => 'hloubka/tloušťka',
        'h.' => 'hloubka/tloušťka',
        'hmot' => 'hmotnost',
        'hmot.' => 'hmotnost',
        'hr' => 'hloubka s rámem',
        'jiný' => 'jiný nespecifikovaný',
        'p' => 'průměr/ráže',
        'p.' => 'průměr/ráže',
        'r.' => 'ráže',
        'ryz' => 'ryzost',
        's' => 'šířka',
        's.' => 'šířka',
        'sd.' => 'šířka grafické desky',
        'sp' => 'šířka s paspartou',
        'sp.' => 'šířka s paspartou',
        'sr' => 'šířka s rámem',
        'v' => 'celková výška/délka',
        'v.' => 'celková výška/délka',
        'vd.' => 'výška grafické desky',
        'vp' => 'výška s paspartou',
        'vp.' => 'výška s paspartou',
        'vr' => 'výška s rámem',
        ';' => ';',
        '=' => ' ',
        'cm' => ' cm',
    ];

    protected static $name = 'mg';

    public function __construct(IFileRepository $repository) {
        parent::__construct($repository);

        $this->filters[] = function (array $record) {
            return $record['Plus2T'] != 'ODPIS';
        };

        $this->sanitizers[] = function ($value) {
            return empty_to_null($value);
        };
    }

    protected function getItemId(array $record) {
        $id = sprintf('CZE:MG.%s_%s', $record['Rada_S'], (int)$record['PorC_S']);
        if ($record['Lomeni_S'] != '_') {
            $id = sprintf('%s-%s', $id, $record['Lomeni_S']);
        }

        return $id;
    }

    protected function getItemImageFilenameFormat(array $record) {
        $filename = sprintf('%s%s', $record['Rada_S'], str_pad($record['PorC_S'], 6, '0', STR_PAD_LEFT));
        if ($record['Lomeni_S'] != '_') {
            $filename = sprintf('%s-%s', $filename, $record['Lomeni_S']);
        }

        return $filename . '{_*,}';
    }

    protected function hydrateIdentifier(array $record) {
        $identifier = sprintf('%s %s', $record['Rada_S'], (int)$record['PorC_S']);
        if ($record['Lomeni_S'] != '_') {
            $identifier = sprintf('%s/%s', $identifier, $record['Lomeni_S']);
        }

        return $identifier;
    }

    protected function hydrateMedium(array $record) {
        return isset($record['Materiál']) && isset($record['MatSpec']) ? ($record['Materiál'] . ', ' . $record['MatSpec']) : $record['Materiál'];
    }

    protected function hydrateTechnique(array $record) {
        return ($record['TechSpec']) ? ($record['Technika'] . ', ' . $record['TechSpec']) : $record['Technika'];
    }

    protected function hydrateWorkType(array $record) {
        return (isset(static::$cz_work_types_spec[$record['Skupina']])) ? static::$cz_work_types_spec[$record['Skupina']] : 'nespecifikované';
    }

    protected function hydrateRelationshipType(array $record) {
        return self::isBiennial($record) ? 'ze souboru' : '';
    }

    protected function hydrateRelatedWork(array $record) {
        return self::isBiennial($record) ? 'Bienále Brno' : '';
    }

    protected function hydrateMeasurement(array $record) {
        return (!empty($record['Služ']) && $record['Služ'] != '=') ? strtr($record['Služ'], static::$cz_measurement_replacements)  : '';
    }

    protected static function isBiennial(array $record) {
        return strpos($record['Okolnosti'], 'BB') !== false;
    }
}