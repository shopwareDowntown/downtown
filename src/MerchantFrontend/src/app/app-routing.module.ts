import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {DashboardComponent} from './views/dashboard/dashboard.component';
import {AuthGuard} from './core/guards/auth.guard';
import {MerchantDetailsComponent} from './views/merchant-details/merchant-details.component';
import {LandingPageLayoutComponent} from './core/layouts/landing-page-layout/landing-page-layout.component';
import {AdminPageLayoutComponent} from './core/layouts/admin-page-layout/admin-page-layout.component';
import { MerchantHomeComponent } from './views/merchant-home/merchant-home.component';
import { MerchantVouchersComponent } from './views/merchant-vouchers/merchant-vouchers.component';
import { OrganizationMerchantListComponent } from './views/organization-merchant-list/organization-merchant-list.component';
import { OrganizationProfileComponent } from './views/organization-profile/organization-profile.component';
import { OrganizationHomeComponent } from './views/organization-home/organization-home.component';
import {OrganizationDisclaimerComponent} from "./views/organization-disclaimer/organization-disclaimer.component";

const routes: Routes = [
  {
    path: '',
    component: LandingPageLayoutComponent,
    children: [
      {path: '', component: DashboardComponent},
      {path: 'reset-password/merchant/:tokenMerchant', component: DashboardComponent},
      {path: 'reset-password/organization/:tokenOrganization', component: DashboardComponent}
    ]
  },

  {
    path: 'merchant',
    component: AdminPageLayoutComponent,
    children: [
      {path: '', redirectTo: 'home', pathMatch: 'prefix', canActivate: [AuthGuard]},
      {path: 'home', component: MerchantHomeComponent, canActivate: [AuthGuard]},
      {path: 'detail/:id', component: MerchantDetailsComponent, canActivate: [AuthGuard]},
      {path: 'profile', component: MerchantDetailsComponent, canActivate: [AuthGuard]},
      {path: 'vouchers', component: MerchantVouchersComponent, canActivate: [AuthGuard]},
      {path: 'products', loadChildren: () => import('./views/merchant-products/merchant-products.module').then(value => value.MerchantProductsModule), canActivate: [AuthGuard]},
      {path: 'delivery', loadChildren: () => import('./views/local-delivery/local-delivery.module').then(value => value.LocalDeliveryModule), canActivate: [AuthGuard]},
      {path: 'orders', loadChildren: () => import('./views/merchant-orders/merchant-orders.module').then(value => value.MerchantOrdersModule), canActivate: [AuthGuard]}

    ]
  },

  {
    path: 'organization',
    component: AdminPageLayoutComponent,
    children: [
      {path: '', redirectTo: 'home', pathMatch: 'prefix', canActivate: [AuthGuard]},
      {path: 'home', component: OrganizationHomeComponent, canActivate: [AuthGuard]},
      {path: 'profile', component: OrganizationProfileComponent, canActivate: [AuthGuard]},
      {path: 'merchants', component: OrganizationMerchantListComponent, canActivate: [AuthGuard]},
      {path: 'disclaimer', component: OrganizationDisclaimerComponent, canActivate: [AuthGuard]}
    ]
  },

  {
    path: '**',
    redirectTo: ''
  }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
