import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FooterFourComponent } from './footer-four.component';

describe('FooterFourComponent', () => {
  let component: FooterFourComponent;
  let fixture: ComponentFixture<FooterFourComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FooterFourComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FooterFourComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
