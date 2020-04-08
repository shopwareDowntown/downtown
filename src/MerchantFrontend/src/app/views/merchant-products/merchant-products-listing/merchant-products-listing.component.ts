import { Component, OnInit } from '@angular/core';
import { MerchantApiService } from '../../../core/services/merchant-api.service';
import { Product } from '../../../core/models/product.model';
import { Router } from '@angular/router';

@Component({
  selector: 'portal-merchant-products-listing',
  templateUrl: './merchant-products-listing.component.html',
  styleUrls: ['./merchant-products-listing.component.scss']
})
export class MerchantProductsListingComponent implements OnInit {

  products: Product[];
  loading: boolean;
  total: number;
  limit = 10;
  offset: number;
  currentPage = 1;
  fromProduct: number;
  tillProduct: number;

  constructor(private merchantService: MerchantApiService, private router: Router) {}

  ngOnInit(): void {
    this.offset = 0;
    this.refresh();
  }

  refresh(): void {
    this.pageChange();
    this.loading = true;
    this.merchantService.getProducts(this.limit, this.offset).subscribe((value) => {
      this.products = value.data;
      this.total = value.total;
      this.pageChange();
      this.loading = false;
    });
  }

  openAddProductForm(): void {
    this.router.navigate(['merchant/products/details']);
  }

  editProduct(product: Product): void {
    this.router.navigate(['merchant/products/details', product.id]);
  }

  pageChange(): void {
    this.offset = (this.currentPage - 1) * 10;

    if (this.currentPage === 1) {
      this.fromProduct = this.fromProduct = 1;
      if (this.total === 0) {
        this.fromProduct = 0;
      }
    } else {
      this.fromProduct = (this.currentPage -1) * this.limit;
    }

    if (this.fromProduct + this.limit <= this.total) {
      this.tillProduct = this.fromProduct + this.limit;
      if (this.fromProduct === 1) {
        this.tillProduct -= 1;
      }
    } else {
      this.tillProduct = this.total;
    }
  }
}
