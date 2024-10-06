<?php

namespace Application\Model;

use Exception;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\Regex;

class Recipient
{
    /** @var $id  */
    public $id;

    /** @var $name  */
    public $name;

    /** @var $phone_number  */
    public $phone_number;

    /** @var $email  */
    public $email;

    /** @var $inputFilter  */
    protected $inputFilter;

    /**
     * Set data method
     *
     * @param array $data
     * @return void
     */
    public function exchangeArray(array $data, $toUpdate = false)
    {
        if (!$toUpdate) {
            $this->id = $data['id'] ?? null;
        }
        $this->name = $data['name'] ?? null;
        $this->phone_number = $data['phone_number'] ?? null;
        $this->email = $data['email'] ?? null;
    }

    /**
     * Get Array of object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
        ];
    }

    /**
     * Set Input Filter method
     *
     * @param InputFilterInterface $inputFilter
     * @return mixed
     * @throws Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new Exception("Not used");
    }

    /**
     * Add Validators
     *
     * @return InputFilter
     */
    public static function getInputFilter()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'name',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'The name is required',
                        ],
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'phone_number',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'The phone number is required',
                        ],
                    ],
                ],
                [
                    'name' => Regex::class,
                    'options' => [
                        'pattern' => '/^\+?\d{10,15}$/',
                        'messages' => [
                            Regex::NOT_MATCH => 'The phone number must be valid (ex: +351912345678)',
                        ],
                    ],
                ],
            ],
        ]);

        return $inputFilter;
    }
}
