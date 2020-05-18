<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToDateTimeTransformer implements DataTransformerInterface
{
    /**
     * Gets DateTime data from symfony form and turn it into string
     * @param \DateTimeInterface $dateTime
     * @return string
     */
    public function transform($dateTime): string
    {
        if ($dateTime == null) {
            return '';
        }

        return $dateTime->format('Y/m/d');
    }

    /**
     * Getting data from the form and transform it into what symfony expects
     * @param string $strDate
     * @return \DateTimeInterface
     */
    public function reverseTransform($strDate): \DateTimeInterface
    {
        if ($strDate === '') {
            throw new TransformationFailedException('You must provide a date');
        }

        $date = \DateTime::createFromFormat('Y/m/d', $strDate);

        if (!$date) {
            throw new TransformationFailedException('invalid date format');
        }

        return $date;
    }
}