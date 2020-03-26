import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {LandingPageLayoutComponent} from './landing-page-layout/landing-page-layout.component';
import {AdminPageLayoutComponent} from './admin-page-layout/admin-page-layout.component';
import {ClarityModule, ClrIconModule, ClrLayoutModule, ClrVerticalNavModule} from '@clr/angular';
import {RouterModule} from '@angular/router';
import { AuthPageLayoutComponent } from './auth-page-layout/auth-page-layout.component';


@NgModule({
  declarations: [LandingPageLayoutComponent, AdminPageLayoutComponent, AuthPageLayoutComponent],
  imports: [
    CommonModule,
    RouterModule,
    ClarityModule
  ],
  exports: [LandingPageLayoutComponent, AdminPageLayoutComponent]
})
export class LayoutsModule {
}
