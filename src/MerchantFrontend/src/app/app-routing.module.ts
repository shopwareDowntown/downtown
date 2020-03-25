import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DashboardComponent } from './views/dashboard/dashboard.component';
import { MerchantRegisterComponent } from './views/merchant-register/merchant-register.component';
import { AuthGuard } from './core/guards/auth.guard';
import { MerchantLoginComponent } from './views/merchant-login/merchant-login.component';
import { MerchantDetailsComponent } from './views/merchant-details/merchant-details.component';
import { OrganizationDetailsComponent } from './views/organization-details/organization-details.component';
import { OrganizationLoginComponent } from './views/organization-login/organization-login.component';
import { OrganizationRegisterComponent } from './views/organization-register/organization-register.component';

const routes: Routes = [
  { path: 'merchant/register', component: MerchantRegisterComponent, canActivate: [AuthGuard] },
  { path: 'merchant/login', component: MerchantLoginComponent, canActivate: [AuthGuard] },
  { path: 'merchant/:id', component: MerchantDetailsComponent, canActivate: [AuthGuard] },
  { path: 'organization/register', component: OrganizationRegisterComponent, canActivate: [AuthGuard] },
  { path: 'organization/login', component: OrganizationLoginComponent, canActivate: [AuthGuard] },
  { path: 'organization/:id', component: OrganizationDetailsComponent, canActivate: [AuthGuard] },
  { path: '', component: DashboardComponent },
  { path: '**', component: DashboardComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {}
