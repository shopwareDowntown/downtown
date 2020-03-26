import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DashboardComponent } from './dashboard.component';
import { SharedModule } from '../../shared/shared.module';
import { CoreModule } from '../../core/core.module';

@NgModule({
  imports: [
    CommonModule,
    SharedModule,
    CoreModule
  ],
  declarations: [
    DashboardComponent
  ],
  exports: [
    DashboardComponent
  ],
})
export class DashboardModule {}
