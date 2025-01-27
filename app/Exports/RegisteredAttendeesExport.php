<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RegisteredAttendeesExport 
	implements FromCollection, WithHeadings, WithStyles
{
	public function __construct(
		private array $attendees, 
		private array $event
	){
	}

  public function collection()
  {
    return new Collection($this->attendees);
  }

  public function headings(): array
  {
    return ['Id', 'Name', 'Company', 'Attendee Type', 'Confirmed'];
  }

  public function styles(Worksheet $sheet)
  {
    // Setarea caption-ului
    $sheet->setCellValue('A1', 'Event: ' . $this->event['name']);
    $sheet->setCellValue('A2', 'Location: ' . $this->event['location']['name']);
    $sheet->setCellValue('A3', 'Start Date: ' . $this->event['start_time']);
    $sheet->setCellValue('A4', 'End Date: ' . $this->event['end_time']);

    // Lăsăm celulele de la B1 la B4, C1 la C4 etc. goale
    for ($col = 'B'; $col <= 'E'; $col++) {
      for ($row = 1; $row <= 4; $row++) {
        $sheet->setCellValue($col . $row, '');
      }
    }

    // Setarea headere-lor începând de la A5
    $sheet->setCellValue('A5', 'Id');
    $sheet->setCellValue('B5', 'Name');
    $sheet->setCellValue('C5', 'Company');
    $sheet->setCellValue('D5', 'Attendee Type');
    $sheet->setCellValue('E5', 'Confirmed');

    // Adăugarea datelor începând de la rândul 6
    $row = 6;
    foreach ($this->attendees as $attendee) {
      $sheet->setCellValue('A' . $row, $attendee['id']);
      $sheet->setCellValue('B' . $row, $attendee['name']);
      $sheet->setCellValue('C' . $row, $attendee['company']);
      $sheet->setCellValue('D' . $row, substr($attendee['attendee_type'], 0, -1));
      $sheet->setCellValue('E' . $row, $attendee['confirmed']);
      $row++;
    }

    // Alinierea la stânga pentru toată coloana A
    $sheet
      ->getStyle('A:A')
      ->getAlignment()
      ->setHorizontal(Alignment::HORIZONTAL_LEFT);

    // Adăugarea bold pentru A5, B5, C5, D5, E5
    $sheet
      ->getStyle('A5:E5')
      ->getFont()
      ->setBold(true);

    return [
      'A1:A4' => [
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
      ],
    ];
  }
}
