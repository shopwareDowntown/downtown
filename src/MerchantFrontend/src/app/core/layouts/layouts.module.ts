import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {LandingPageLayoutComponent} from './landing-page-layout/landing-page-layout.component';
import {AdminPageLayoutComponent} from './admin-page-layout/admin-page-layout.component';
import {ClarityModule, ClrIconModule, ClrLayoutModule, ClrVerticalNavModule} from '@clr/angular';
import {RouterModule} from '@angular/router';
import { MerchantLoginModule } from '../../views/merchant-login/merchant-login.module';


@NgModule({
  declarations: [LandingPageLayoutComponent, AdminPageLayoutComponent],
  imports: [
    CommonModule,
    RouterModule,
    ClarityModule,
    MerchantLoginModule
  ],
  exports: [LandingPageLayoutComponent, AdminPageLayoutComponent]
})
export class LayoutsModule {
}
