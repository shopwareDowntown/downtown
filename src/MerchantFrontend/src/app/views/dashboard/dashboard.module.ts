import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DashboardComponent } from './dashboard.component';
import { SharedModule } from '../../shared/shared.module';
import { CoreModule } from '../../core/core.module';
import { ClrIconModule, ClrModalModule } from '@clr/angular';

@NgModule({
  imports: [
    CommonModule,
    SharedModule,
    CoreModule,
    ClrIconModule,
    ClrModalModule
  ],
  declarations: [
    DashboardComponent
  ],
  exports: [
    DashboardComponent
  ],
})
export class DashboardModule {}
