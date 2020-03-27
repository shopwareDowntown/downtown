import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {LocalDeliveryComponent} from "./local-delivery.component";
import {CreatePackageComponent} from "./create-package/create-package.component";

const routes: Routes = [
  {
    path: '',
    component: LocalDeliveryComponent
  },
  {
    path: 'create',
    component: CreatePackageComponent
  }
  // TODO: add detail page
  // {
  //   path: ':id',
  //   component: DeliveryPackageDetailComponent,
  //   resolve: {
  //     product: DeliveryPackageResolver
  //   }
  // }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LocalDeliveryRoutingModule {
}
