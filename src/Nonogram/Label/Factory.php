<?php

namespace Nonogram\Label;

class Factory implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    /**
     * @var LabelProviderCells
     */
    private $labelProviderCells;

    /**
     * Factory constructor.
     * @param LabelProviderCells $labelProviderCells
     */
    public function __construct(LabelProviderCells $labelProviderCells)
    {
        $this->labelProviderCells = $labelProviderCells;
    }

    /**
     * @param array $cells
     * @return Label
     */
    public function getForCells(array $cells)
    {
        $labelsRaw = $this->labelProviderCells->generateLabels($cells);
        return $this->getFromRaw($labelsRaw);
    }

    /**
     * @param array $labelsRaw
     * @return \Nonogram\Label\Label
     */
    public function getFromRaw(array $labelsRaw)
    {
        if (empty($labelsRaw) || !isset($labelsRaw['columns']) || !isset($labelsRaw['rows'])) {
            throw new \InvalidArgumentException('empty label array');
        }

        $colored = false;
        foreach(array('columns', 'rows') as $direction) {
            foreach ($labelsRaw[$direction] as $sequence) {
                foreach($sequence as $count) {
                    $colored = $count instanceof Count;
                    break 3;
                }
            }
        }

        $label = $this->container->get($colored ? 'label_colored' : 'label');

        $label->setCol($labelsRaw['columns']);
        $label->setRow($labelsRaw['rows']);

        return $label;
    }
}
