import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {LayoutsModule} from './layouts/layouts.module';

@NgModule({
  imports: [
    CommonModule,
    LayoutsModule
  ],
  declarations: [],
  exports: [
    LayoutsModule
  ],
})
export class CoreModule {
}
