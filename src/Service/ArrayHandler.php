<?php
namespace Quartet\Silex\Service;

class ArrayHandler
{
    const ASC = 'asc';
    const DESC = 'desc';

    const PARTIAL = 'partial';
    const PERFECT = 'perfect';

    /**
     * @param array $target target array.
     * @param string $sort key to sort.
     * @param string $direction self::ASC or self::DESC.
     */
    public function sort(array $target, $sort, $direction)
    {
        $columns = array();

        foreach ($target as $index => $row) {
            foreach ($row as $key => $value) {
                $columns[$key][$index] = $value;
            }
        }

        if (isset($columns[$sort])) {
            array_multisort($columns[$sort], $direction === self::ASC ? SORT_ASC : SORT_DESC, SORT_NATURAL, $target);
        }

        return $target;
    }

    /**
     * @param array $target target array.
     * @param $field target field name.
     * @param $query query value.
     * @param string $matchType self:PARTIAL or self::PERFECT. defaults to selft::PARTIAL.
     */
    public function filter(array $target, $field, $query, $matchType = self::PARTIAL)
    {
        if (is_null($field) || is_null($query) || $field === '' || $query === '') {
            return $target;
        }

        $filtered = array_filter($target, function ($row) use ($field, $query, $matchType) {
            foreach ($row as $key => $value) {
                if ($key === $field) {
                    if ($matchType === self::PARTIAL && strpos($value, $query) !== false) {
                        return true;
                    }
                    if ($matchType === self::PERFECT && trim($value) === trim($query)) {
                        return true;
                    }
                    return false;
                }
            }
        });

        return $filtered;
    }
}
