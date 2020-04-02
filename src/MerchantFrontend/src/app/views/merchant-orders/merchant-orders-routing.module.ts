import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from '../../core/guards/auth.guard';
import { MerchantOrdersListingComponent } from './merchant-orders-listing/merchant-orders-listing.component';

const routes: Routes = [
  {
    path: '',
    component: MerchantOrdersListingComponent,
    canActivate: [AuthGuard]
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MerchantOrdersRoutingModule {
}
