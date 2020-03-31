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

  constructor(private merchantService: MerchantApiService, private router: Router) {}

  ngOnInit(): void {
    this.offset = 0;
    this.refresh();
    this.currentPage;
  }

  refresh(): void {
    this.pageChange();
    console.log(this.offset);
    this.loading = true;
    this.merchantService.getProducts(this.limit, this.offset).subscribe((value) => {
      this.products = value.data;
      this.total = value.total;
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
  }
}
