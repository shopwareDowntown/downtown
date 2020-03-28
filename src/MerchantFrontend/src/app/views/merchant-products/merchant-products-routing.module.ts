import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {MerchantProductsListingComponent} from './merchant-products-listing/merchant-products-listing.component';
import {AuthGuard} from '../../core/guards/auth.guard';
import {MerchantProductsDetailComponent} from './merchant-products-detail/merchant-products-detail.component';


const routes: Routes = [
  {
    path: '',
    component: MerchantProductsListingComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'details',
    component: MerchantProductsDetailComponent,
    canActivate: [AuthGuard],
  },
  {
    path: 'details/:id',
    component: MerchantProductsDetailComponent,
    canActivate: [AuthGuard]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MerchantProductsRoutingModule {
}
