import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Product } from '../../../core/models/product.model';
import { MerchantApiService } from '../../../core/services/merchant-api.service';
import { switchMap } from 'rxjs/operators';
import { Observable, of, Subject } from 'rxjs';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ToastService } from '../../../core/services/toast.service';

@Component({
  selector: 'portal-merchant-products-detail',
  templateUrl: './merchant-products-detail.component.html',
  styleUrls: ['./merchant-products-detail.component.scss']
})
export class MerchantProductsDetailComponent implements OnInit{

  product: Product = null;
  form: FormGroup;
  newProduct = false;
  saveInProgress = false;

  constructor(
    private readonly activeRoute: ActivatedRoute,
    private readonly merchantApiService: MerchantApiService,
    private readonly router: Router,
    private readonly formBuilder: FormBuilder,
    private readonly toastService: ToastService
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
        this.newProduct = true;
        this.product = <Product>{
          name: '',
          description: '',
          productType: '',
          price: 0,
          tax: 19,
          active: false,
          media: []
        }
      }
      this.initializeForm();
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
    });
    this.form.get('productType').valueChanges.subscribe((value: string) => {
      if (value === 'storeWindow') {
        this.form.get('price').setValidators([]);
      } else {
        this.form.get('price').setValidators(Validators.required);
      }
      this.form.get('price').updateValueAndValidity();
    })
  }

  saveProduct(): void {
    this.saveInProgress = true;
    let product$ = null;
    if(this.form.get('productType').value === 'storeWindow') {
      this.form.get('price').setValue(0);
    }
    if (this.product.id) {
      product$ = this.merchantApiService.updateProduct(this.form.value);
    } else {
      product$ = this.merchantApiService.addProduct(this.form.value);
    }
    product$.pipe(
      switchMap((product: {data: Product}) => {
        if (this.form.get('media').value !== null) {
          return this.merchantApiService.addImageToProduct(this.form.get('media').value, product.data.id);
        } else {
          return of(product);
        }
      })).subscribe((product: {data: Product}) => {
        let toastMessage;
        if (this.newProduct === true) {
          toastMessage = 'Produkt erstellt';
          this.newProduct = false;
        } else {
          toastMessage = 'Produkt aktualisiert';
        }
        this.form.get('id').setValue(product.data.id);
        this.form.get('media').setValue(null);
        this.product = product.data;
        this.toastService.success(toastMessage);
        this.saveInProgress = false;
    },
      () => {
        this.toastService.error('Fehler', 'Beim Speichern des Produktes ist ein Fehler aufgetreten.');
        this.saveInProgress = false;
      },
      );
  }

  imageSelected(value: any) {
    this.form.get('media').setValue(value);
  }
}
