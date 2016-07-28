<?php
namespace EdusohoNet\Service\Common;

use PHPExcel;
use AdminUI\Util\DataDict;

class PHPExcelToolkit
{
    public static function exportUser($data, $info)
    {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator($info['creator'])
                             ->setLastModifiedBy($info['creator'])
                             ->setTitle('Office 2007 XLSX Document')
                             ->setSubject('Office 2007 XLSX Document')
                             ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
                             ->setKeywords('office 2007 openxml php')
                             ->setCategory('export file');
        $activieSheet = $objPHPExcel->setActiveSheetIndex(0);
        $index = 0;
        foreach ($info['title'] as $key => $value) {
            $char = chr(65+$index++);
            $activieSheet->setCellValue("{$char}1", $value);
            $activieSheet->getColumnDimension($char)->setWidth(14);
        }

        $activieSheet->getRowDimension('1')->setRowHeight(18);

        if (!empty($data)) {
            $index = 2;
            foreach ($data as $one) {
                $i = 0;
                foreach ($info['title'] as $key => $value) {
                    $cellValue = $one[$key];
                    if ($key == 'createdTime') {
                        $cellValue = date('Y-m-d', $cellValue);
                    }
                    if ($key == 'level') {
                        $cellValue = DataDict::text('user_level', $cellValue);
                    }
                    $char = chr(65+$i++);
                    $activieSheet->setCellValue("{$char}{$index}", $cellValue);
                }
                $activieSheet->getRowDimension($index)->setRowHeight(18);
                $index++;
            }
        }
        $objPHPExcel->getActiveSheet()->setTitle('用户信息');
        $objPHPExcel->setActiveSheetIndex(0);
        $filename = urlencode('用户信息表').'_'.date('Y-m-dHis');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $objWriter->save('php://output');
        exit();
    }

    public static function exportTrialCustomer($data, $info)
    {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator($info['creator'])
                             ->setLastModifiedBy($info['creator'])
                             ->setTitle('Office 2007 XLSX Document')
                             ->setSubject('Office 2007 XLSX Document')
                             ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
                             ->setKeywords('office 2007 openxml php')
                             ->setCategory('export file');
        $activieSheet = $objPHPExcel->setActiveSheetIndex(0);
        $index = 0;
        foreach ($info['title'] as $key => $value) {
            $char = chr(65+$index++);
            $activieSheet->setCellValue("{$char}1", $value);
            $activieSheet->getColumnDimension($char)->setWidth(14);
        }

        $activieSheet->getRowDimension('1')->setRowHeight(18);

        if (!empty($data)) {
            $index = 2;
            foreach ($data as $one) {
                $i = 0;
                foreach ($info['title'] as $key => $value) {
                    $cellValue = $one[$key];
                    switch($key){
                        case 'createdTime' :
                            $cellValue = date('Y-m-d H:i', $cellValue);
                            break;
                        case 'lastLoginTime':
                            $cellValue = date('Y-m-d H:i', $cellValue);
                            break;
                        default:
                            break;
                    }
                    $char = chr(65+$i++);
                    $activieSheet->setCellValue("{$char}{$index}", $cellValue);
                }
                $activieSheet->getRowDimension($index)->setRowHeight(18);
                $index++;
            }
        }
        $objPHPExcel->getActiveSheet()->setTitle('一键试用-客户名单');
        $objPHPExcel->setActiveSheetIndex(0);
        $filename = urlencode('一键试用-客户名单').'_'.date('Y-m-dHis');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $objWriter->save('php://output');
        exit();
    }
}
