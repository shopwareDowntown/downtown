import { Component } from '@angular/core';

@Component({
  selector: 'portal-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.scss']
})
export class HeaderComponent {

  readonly navigation = [
    {route: '', title: 'Dashboard'},
    {route: 'merchant/login', title: 'Merchant Login'},
  ]

}
