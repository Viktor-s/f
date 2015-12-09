<?php

namespace Furniture\CommonBundle\HttpFoundation;

use Symfony\Component\HttpFoundation\Response;

class PhpExcelResponse extends Response
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
     * @param string                   $format
     * @param int                      $status
     * @param array                    $headers
     */
    public function __construct(\PHPExcel_Writer_IWriter $writer, $fileName, $format, $status = 200, array $headers = [])
    {
        parent::__construct(null, $status, $headers);

        $fileName = str_replace('"', '\"', $fileName);

        $this->writer = $writer;
        $this->headers->set('Content-Disposition',  'attachment; filename="' . $fileName . '"');

        switch ($format) {
            case 'excel':
                $this->headers->set('Content-Type', 'application/vnd.ms-excel');
                break;

            case 'pdf':
                $this->headers->set('Content-Type', 'application/pdf');
                break;

            default:
                throw new \InvalidArgumentException(sprintf(
                    'Invalid format "%s". Available formats: "excel" or "pdf".',
                    $format
                ));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function sendContent()
    {
        $this->writer->save('php://output');
    }
}
