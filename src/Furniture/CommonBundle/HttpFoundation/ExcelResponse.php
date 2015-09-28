<?php

namespace Furniture\CommonBundle\HttpFoundation;

use Symfony\Component\HttpFoundation\Response;

class ExcelResponse extends Response
{
    /**
     * @var \PHPExcel_Writer_IWriter
     */
    private $writer;

    /**
     * Construct
     *
     * @param \PHPExcel_Writer_IWriter $writer
     * @param string                   $fileName
     * @param int                      $status
     * @param array                    $headers
     */
    public function __construct(\PHPExcel_Writer_IWriter $writer, $fileName, $status = 200, array $headers = [])
    {
        parent::__construct(null, $status, $headers);

        $fileName = str_replace('"', '\"', $fileName);

        $this->writer = $writer;
        $this->headers->set('Content-Disposition',  'attachment; filename="' . $fileName . '"');
    }

    /**
     * {@inheritDoc}
     */
    public function sendContent()
    {
        $this->writer->save('php://output');
    }
}
