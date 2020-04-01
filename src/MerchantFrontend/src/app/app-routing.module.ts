import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {DashboardComponent} from './views/dashboard/dashboard.component';
import {AuthGuard} from './core/guards/auth.guard';
import {MerchantDetailsComponent} from './views/merchant-details/merchant-details.component';
import {OrganizationDetailsComponent} from './views/organization-details/organization-details.component';
import {LandingPageLayoutComponent} from './core/layouts/landing-page-layout/landing-page-layout.component';
import {AdminPageLayoutComponent} from './core/layouts/admin-page-layout/admin-page-layout.component';
import { MerchantHomeComponent } from './views/merchant-home/merchant-home.component';

const routes: Routes = [
  {
    path: '',
    component: LandingPageLayoutComponent,
    children: [
      {path: '', component: DashboardComponent},
      {path: 'reset-password/:token', component: DashboardComponent}
    ]
  },

  {
    path: 'merchant',
    component: AdminPageLayoutComponent,
    children: [
      {path: 'home', component: MerchantHomeComponent, canActivate: [AuthGuard]},
      {path: 'detail/:id', component: MerchantDetailsComponent, canActivate: [AuthGuard]},
      {path: 'profile', component: MerchantDetailsComponent, canActivate: [AuthGuard]},
      {path: 'products', loadChildren: () => import('./views/merchant-products/merchant-products.module').then(value => value.MerchantProductsModule), canActivate: [AuthGuard]},
      {path: 'delivery', loadChildren: () => import('./views/local-delivery/local-delivery.module').then(value => value.LocalDeliveryModule), canActivate: [AuthGuard]}
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
