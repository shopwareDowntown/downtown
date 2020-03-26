import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {DashboardComponent} from './views/dashboard/dashboard.component';
import {MerchantRegisterComponent} from './views/merchant-register/merchant-register.component';
import {AuthGuard} from './core/guards/auth.guard';
import {MerchantLoginComponent} from './views/merchant-login/merchant-login.component';
import {MerchantDetailsComponent} from './views/merchant-details/merchant-details.component';
import {OrganizationDetailsComponent} from './views/organization-details/organization-details.component';
import {OrganizationLoginComponent} from './views/organization-login/organization-login.component';
import {OrganizationRegisterComponent} from './views/organization-register/organization-register.component';
import {LandingPageLayoutComponent} from './core/layouts/landing-page-layout/landing-page-layout.component';
import {AdminPageLayoutComponent} from './core/layouts/admin-page-layout/admin-page-layout.component';
import {AuthPageLayoutComponent} from './core/layouts/auth-page-layout/auth-page-layout.component';

const routes: Routes = [
  {
    path: '',
    component: LandingPageLayoutComponent,
    children: [
      {path: '', component: DashboardComponent},
    ]
  },

  {
    path: 'login',
    component: AuthPageLayoutComponent,
    children: [
      {path: 'merchant', component: MerchantLoginComponent, canActivate: [AuthGuard]},
      {path: 'organization', component: OrganizationLoginComponent, canActivate: [AuthGuard]},
    ]
  },

  {
    path: 'register',
    component: AuthPageLayoutComponent,
    children: [
      {path: 'merchant', component: MerchantRegisterComponent, canActivate: [AuthGuard]},
      {path: 'organization', component: OrganizationRegisterComponent, canActivate: [AuthGuard]},
    ]
  },

  {
    path: 'merchant',
    component: AdminPageLayoutComponent,
    children: [
      {path: 'detail/:id', component: MerchantDetailsComponent, canActivate: [AuthGuard]},
      {path: 'profile', component: MerchantDetailsComponent, canActivate: [AuthGuard]},
      {path: 'settings', component: MerchantDetailsComponent, canActivate: [AuthGuard]},
      {path: 'products', loadChildren: () => import('./views/merchant-products/merchant-products.module').then(value => value.MerchantProductsModule), canActivate: [AuthGuard]}
    ]
  },

  {
    path: 'organization',
    component: AdminPageLayoutComponent,
    children: [
      {path: 'detail/:id', component: OrganizationDetailsComponent, canActivate: [AuthGuard]},
      {path: 'profile', component: OrganizationDetailsComponent, canActivate: [AuthGuard]},
      {path: 'settings', component: OrganizationDetailsComponent, canActivate: [AuthGuard]}
    ]
  },

  {path: '**', component: DashboardComponent}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
