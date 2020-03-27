import { AfterViewInit, Component, ElementRef, ViewChild, Input } from '@angular/core';
import { environment } from "../../../environments/environment";

@Component({
  selector: 'portal-organization-register',
  templateUrl: './organization-register.component.html',
  styleUrls: ['./organization-register.component.scss']
})
export class OrganizationRegisterComponent implements AfterViewInit {

  @ViewChild('hubspotForm', { static: false }) hubspotForm: ElementRef;
  @Input() registerModalOpen = true;

  constructor() {}

  ngAfterViewInit(): void {
    const portalId = environment.hubspotPortalId;
    const formId = environment.hubspotFormId;
    let hubspotScript = document.createElement('script');
    hubspotScript.innerHTML = 'hbspt.forms.create({portalId: "' + portalId + '",formId: "' + formId + '"})';
    this.hubspotForm.nativeElement.appendChild(hubspotScript);
  }

  registerModalClosed() {
    this.registerModalOpen = false;
  }
}
