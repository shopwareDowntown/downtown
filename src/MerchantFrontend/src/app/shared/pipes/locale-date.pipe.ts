import { Pipe, PipeTransform } from '@angular/core';
import { DatePipe } from '@angular/common';

@Pipe({
  name: 'localeDate'
})
export class LocaleDatePipe implements PipeTransform {

  transform(value: any): unknown {
    const localeDate: DatePipe = new DatePipe('de_DE');
    return localeDate.transform(value, 'medium');
  }

}
