<?php

namespace Nonogram\View;

use Nonogram\Cell\AnyCell;

/**
 * Class ViewXml
 *
 * Outputs format as described in http://webpbn.com/pbn_fmt.html
 * Allows storing of *all* relevant information about puzzles and is
 * therefore the preferred storage method
 *
 * @package Nonogram\View
 */
class ViewXml extends AbstractView implements ViewInterface, ViewWritableInterface {

    /**
     * @return string
     */
    public function drawField() {
        $outStr = 
            '<?xml version="1.0"?>' . PHP_EOL .
            '<!DOCTYPE pbn SYSTEM "http://webpbn.com/pbn-0.3.dtd">' . PHP_EOL .
            PHP_EOL .
            '<puzzleset>' . PHP_EOL .
            PHP_EOL .
            '<puzzle type="grid" defaultcolor="black">' . PHP_EOL .
            PHP_EOL .
            '<source>webpbn.com</source>' . PHP_EOL .
            '<id>'.($this->grid->getId() > 0 ? '#'.$this->grid->getId():'').'</id>' . PHP_EOL .
            '<title>'.$this->grid->getTitle().'</title>' . PHP_EOL .
            '<author>'.$this->grid->getAuthor().'</author>' . PHP_EOL .
            '<authorid></authorid>' . PHP_EOL .
            '<copyright>'.str_replace('(c) ', '&copy; ', $this->grid->getCopyright()).'</copyright>' . PHP_EOL;
        if($this->grid->getDescription()) {
            $outStr .= '<description>' . PHP_EOL .
                $this->grid->getDescription() . PHP_EOL .
                '</description>' . PHP_EOL;
        }
        $outStr .= PHP_EOL .
            '<color name="white" char=".">fff</color>' . PHP_EOL .
            '<color name="black" char="X">000</color>' . PHP_EOL .
            PHP_EOL .
            '<clues type="columns">' . PHP_EOL;

        $labels = $this->grid->getLabels();
        foreach($labels->getCol() as $col) {
            if(empty($col)) {
                $outStr .= '<line></line>' . PHP_EOL;
                continue;
            }
            $outStr .= '<line><count>' . implode('</count><count>', $col) . '</count></line>' . PHP_EOL;
        }

        $outStr .=
            '</clues>' . PHP_EOL .
            PHP_EOL .
            '<clues type="rows">' . PHP_EOL;

        foreach($labels->getRow() as $row) {
            if(empty($row)) {
                $outStr .= '<line></line>' . PHP_EOL;
                continue;
            }
            $outStr .= '<line><count>' . implode('</count><count>', $row) . '</count></line>' . PHP_EOL;
        }

        $outStr .= '</clues>' . PHP_EOL;
        if($this->grid->isSolved()) {
            $outStr .= PHP_EOL .
            '<solution type="goal">' . PHP_EOL .
            '<image>' . PHP_EOL;

            $field = $this->grid->getCells();
            foreach ($field as $row) {
                $outStr .= '|';
                foreach ($row as $cell) {
                    $outStr .= $cell->getType() === AnyCell::TYPE_BOX ? 'X' : '.';
                }
                $outStr .= '|' . PHP_EOL;
            }

            $outStr .=
                '</image>' . PHP_EOL .
                '</solution>' . PHP_EOL;
        }
        $outStr .=
            PHP_EOL .
            '</puzzle>' . PHP_EOL .
            '</puzzleset>';


        return $outStr;
    }

    /**
     * In case output format supports being written to a file, this method returns the suitable file extension
     * @return string
     */
    public function getFileExtension()
    {
        return 'xml';
    }

}