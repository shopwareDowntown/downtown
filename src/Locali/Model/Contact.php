<?php


namespace Shopware\Production\Locali\Model;


class Contact
{

    public $phone;

    public $mobile;

    public $mail;

    public $fax;

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     * @return $this
     */
    public function setPhone($phone): Contact
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $mobile
     * @return $this
     */
    public function setMobile($mobile): Contact
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     * @return $this
     */
    public function setMail($mail): Contact
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param mixed $fax
     * @return $this
     */
    public function setFax($fax): Contact
    {
        $this->fax = $fax;

        return $this;
    }

}
