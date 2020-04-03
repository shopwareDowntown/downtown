import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from '../../core/guards/auth.guard';
import { MerchantOrdersListingComponent } from './merchant-orders-listing/merchant-orders-listing.component';
import { MerchantOrdersDetailsComponent } from './merchant-orders-details/merchant-orders-details.component';

const routes: Routes = [
  {
    path: '',
    component: MerchantOrdersListingComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'details/:id',
    component: MerchantOrdersDetailsComponent,
    canActivate: [AuthGuard]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MerchantOrdersRoutingModule {
}
