import {Component, OnDestroy, OnInit} from '@angular/core';
import {MerchantApiService} from '../../../core/services/merchant-api.service';
import {Subscription} from 'rxjs';
import {Product} from '../../../core/models/product.model';

@Component({
  selector: 'portal-merchant-products-listing',
  templateUrl: './merchant-products-listing.component.html',
  styleUrls: ['./merchant-products-listing.component.scss']
})
export class MerchantProductsListingComponent implements OnInit, OnDestroy {

  products: Product[];
  loading: boolean;

  // Subscription
  private subProducts: Subscription;

  constructor(private merchantService: MerchantApiService) {
    this.subProducts = merchantService.getProducts().subscribe(value => this.products = value);
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    if (this.subProducts) {
      this.subProducts.unsubscribe();
    }
  }

}
