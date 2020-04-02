import { Component, OnInit } from '@angular/core';
import { MerchantApiService } from '../../../core/services/merchant-api.service';
import { Order } from '../../../core/models/order.model';

@Component({
  selector: 'portal-merchant-orders-listing',
  templateUrl: './merchant-orders-listing.component.html',
  styleUrls: ['./merchant-orders-listing.component.scss']
})
export class MerchantOrdersListingComponent implements OnInit {

  loading: boolean;
  orders: any;
  // limit = 10;
  // offset: number;
  // currentPage = 1;
  // fromProduct: number;
  // tillProduct: number;

  constructor(private readonly merchantApiService: MerchantApiService) { }

  ngOnInit(): void {
     this.refresh();
  }

  refresh() {
    this.loading = true;
    // this.pageChange();
    this.merchantApiService.getOrders().subscribe((orders: Order[]) => {
      this.orders = orders;
      // this.paginationChange();
      this.loading = false;
    });
  }

  openDetails(order: Order) {

  }

  markOrderAsCompleted(order: Order) {

  }

  // pageChange(): void {
  //   this.offset = (this.currentPage - 1) * 10;
  // }
  //
  // paginationChange(): void {
  //   if (this.currentPage === 1) {
  //     this.fromProduct = this.fromProduct = 1;
  //   } else {
  //     this.fromProduct = (this.currentPage -1) * this.limit;
  //   }
  //
  //   if (this.fromProduct + this.limit <= this.total) {
  //     this.tillProduct = this.fromProduct + this.limit;
  //     if (this.fromProduct === 1) {
  //       this.tillProduct -= 1;
  //     }
  //   } else {
  //     this.tillProduct = this.total;
  //   }
  // }
}
