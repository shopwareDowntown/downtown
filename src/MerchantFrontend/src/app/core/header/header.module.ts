import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {HeaderComponent} from './header.component';
import {RouterModule} from '@angular/router';
import {ClrLayoutModule} from '@clr/angular';

@NgModule({
  imports: [
    CommonModule,
    RouterModule,
    ClrLayoutModule
  ],
  declarations: [
    HeaderComponent
  ],
  exports: [
    HeaderComponent
  ],
})
export class HeaderModule {
}
