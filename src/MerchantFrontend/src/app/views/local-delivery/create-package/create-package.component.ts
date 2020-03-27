import {Component, OnInit} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from "@angular/forms";
import {DeliveryBoy} from "../../../core/models/delivery-boy.model";
import { Router } from '@angular/router';

@Component({
  selector: 'portal-create-package',
  templateUrl: './create-package.component.html',
  styleUrls: ['./create-package.component.scss']
})
export class CreatePackageComponent implements OnInit {

  deliveryBoys: DeliveryBoy[] = [];
  shippingMethods: string[] = [];
  isCreating: boolean;
  showDeliveryBoySelect: boolean = false;
  packageCreationForm: FormGroup;

  constructor(private formBuilder: FormBuilder, private router: Router) {
    this.setInitialCreationForm();
  }

  ngOnInit(): void {
    // TODO: get delivery boys with the same zipcode as merchant
    // TODO: get shipping methods
  }

  private setInitialCreationForm(): void {
    this.packageCreationForm = this.formBuilder.group({
      recipientTitle: [null],
      recipientFirstName: [null, Validators.required],
      recipientLastName: [null, Validators.required],
      recipientStreet: [null, Validators.required],
      recipientZipcode: [null, Validators.required],
      recipientCity: [null, Validators.required],
      content: [null, Validators.required],
      comment: [null],
      price: [null, [Validators.required, Validators.pattern(/^\d{1,8}([.,]\d{2})?$/)]],
      deliveryBoy: [null],
      shippingMethod: [null, Validators.required],
    });

    this.packageCreationForm.get('shippingMethod').valueChanges.subscribe((val) => {
      // TODO: if shippingMethod is deliveryboy
    });
  }

  createPackage() {
  }

  dismiss() {
    this.router.navigate(['merchant/delivery']);
  }
}
