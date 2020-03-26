import {Component, OnInit} from '@angular/core';
import {NAVIGATION_LANDING} from '../../navigation';

@Component({
  selector: 'portal-auth-page-layout',
  templateUrl: './auth-page-layout.component.html',
  styleUrls: ['./auth-page-layout.component.scss']
})
export class AuthPageLayoutComponent implements OnInit {

  readonly navigation = NAVIGATION_LANDING;

  constructor() {
  }

  ngOnInit(): void {
  }

}
