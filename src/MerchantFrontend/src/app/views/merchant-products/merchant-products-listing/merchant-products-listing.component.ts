import {Component, OnDestroy, OnInit} from '@angular/core';
import {MerchantApiService} from '../../../core/services/merchant-api.service';
import {Subscription} from 'rxjs';
import {Product} from '../../../core/models/product.model';
import { NavigationExtras, Router } from '@angular/router';
import { ClrDatagridStateInterface } from '@clr/angular';

@Component({
  selector: 'portal-merchant-products-listing',
  templateUrl: './merchant-products-listing.component.html',
  styleUrls: ['./merchant-products-listing.component.scss']
})
export class MerchantProductsListingComponent {

  products: Product[];
  loading: boolean;
  total: number;

  constructor(private merchantService: MerchantApiService, private router: Router) {
    this.refresh();
  }

  refresh() {
    this.loading = true;
    this.merchantService.getProducts().subscribe((value) => {
      this.products = value.data;
      this.total = 100;
      this.loading = false;
    });
  }

  openAddProductForm(): void {
    this.router.navigate(['merchant/products/details']);
  }
}
