import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { OrganizationProfileComponent } from './organization-profile.component';
import {SharedModule} from "../../shared/shared.module";
import {ClrCommonFormsModule, ClrInputModule, ClrTextareaModule} from "@clr/angular";
import {ReactiveFormsModule} from "@angular/forms";



@NgModule({
  declarations: [OrganizationProfileComponent],
  imports: [
    CommonModule,
    SharedModule,
    ClrCommonFormsModule,
    ReactiveFormsModule,
    ClrTextareaModule,
    ClrInputModule
  ]
})
export class OrganizationProfileModule { }
