import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'portal-local-delivery',
  templateUrl: './local-delivery.component.html',
  styleUrls: ['./local-delivery.component.scss']
})
export class LocalDeliveryComponent implements OnInit {

  showDeliveries: boolean = false;

  constructor(private router: Router) { }

  ngOnInit(): void {
    // TODO: fetch deliveries and set showDeliveries to true
    this.showDeliveries = true;
  }

  openCreateDeliveryPackagePage(): void {
    this.router.navigate(['merchant/delivery/create']);
  }
}
