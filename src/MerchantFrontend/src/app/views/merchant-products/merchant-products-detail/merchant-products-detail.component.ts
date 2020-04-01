import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Product } from '../../../core/models/product.model';
import { MerchantApiService } from '../../../core/services/merchant-api.service';
import { switchMap } from 'rxjs/operators';
import { Observable, of, Subject } from 'rxjs';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'portal-merchant-products-detail',
  templateUrl: './merchant-products-detail.component.html',
  styleUrls: ['./merchant-products-detail.component.scss']
})
export class MerchantProductsDetailComponent implements OnInit{

  product: Product = null;
  form: FormGroup;

  constructor(
    private readonly activeRoute: ActivatedRoute,
    private readonly merchantApiService: MerchantApiService,
    private readonly router: Router,
    private readonly formBuilder: FormBuilder
  ) {}

  ngOnInit(): void {
    this.activeRoute.params.pipe(
      switchMap((value) => {
          if (value && value.id) {
            return this.merchantApiService.getProduct(value.id);
          } else {
            return of(false);
          }
        }
      )
    ).subscribe((product: { data: Product }) => {
      if(product && product.data) {
        this.product = product.data;
      } else {
        this.product = <Product>{
          name: '',
          description: '',
          productType: '',
          price: 0,
          tax: 19,
          active: false,
        }
      }
      this.initializeForm()
    });
  }

  initializeForm(): void {
    this.form = this.formBuilder.group({
      id: [this.product.id],
      name: [this.product.name, [Validators.required, Validators.minLength(1)]],
      description: [this.product.description],
      productType: [this.product.productType, Validators.required],
      price: [this.product.price],
      tax: [this.product.tax],
      active: [this.product.active, Validators.required],
      media: [null]
    })
  }

  saveProduct(): void {
    let product$ = null;
    if (this.product.id) {
      product$ = this.merchantApiService.updateProduct(this.form.value);
    } else {
      product$ = this.merchantApiService.addProduct(this.form.value)
    }
    product$.pipe(
      switchMap((product: {data: Product}) => {
        if (this.form.get('media') !== null) {
          return this.merchantApiService.addImageToProduct(this.form.get('media').value, product.data.id)
        } else {
          return of(product);
        }
      })).subscribe((product: {data: Product}) => {
      this.product = product.data;
    });
  }

  imageSelected(value: any) {
    this.form.get('media').setValue(value);
  }
}
