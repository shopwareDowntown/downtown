import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LocalDeliveryComponent } from './local-delivery.component';
import { CreatePackageComponent } from "./create-package/create-package.component";
import { ClarityModule } from "@clr/angular";
import { ReactiveFormsModule } from "@angular/forms";
import { LocalDeliveryRoutingModule } from './local-delivery-routing.module';

@NgModule({
  imports: [
    CommonModule,
    ClarityModule,
    ReactiveFormsModule,
    LocalDeliveryRoutingModule
  ],
  declarations: [
    LocalDeliveryComponent,
    CreatePackageComponent
  ],
  exports: [
    LocalDeliveryComponent,
    CreatePackageComponent
  ],
})
export class LocalDeliveryModule {}
