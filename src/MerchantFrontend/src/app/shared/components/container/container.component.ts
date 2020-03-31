import { Component, Input, HostBinding } from '@angular/core';

@Component({
  selector: 'portal-container',
  templateUrl: './container.component.html',
  styleUrls: ['./container.component.scss']
})
export class ContainerComponent {
  @HostBinding('class.centered') @Input() centered: boolean = true;
}
