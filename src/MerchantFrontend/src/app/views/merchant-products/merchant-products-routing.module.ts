import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {MerchantProductsListingComponent} from './merchant-products-listing/merchant-products-listing.component';
import {AuthGuard} from '../../core/guards/auth.guard';
import {MerchantProductsDetailComponent} from './merchant-products-detail/merchant-products-detail.component';
import {MerchantProductResolver} from './merchant-product.resolver';


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
    path: ':id',
    component: MerchantProductsDetailComponent,
    canActivate: [AuthGuard],
    resolve: {
      product: MerchantProductResolver
    }
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MerchantProductsRoutingModule {
}
