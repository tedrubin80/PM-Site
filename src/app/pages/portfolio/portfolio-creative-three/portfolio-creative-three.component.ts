import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NavbarComponent } from '../../../components/navbar/navbar/navbar.component';
import { CreativePortfolioComponent } from '../../../components/portfolio/creative-portfolio/creative-portfolio.component';
import { FooterFiveComponent } from '../../../components/footer/footer-five/footer-five.component';

@Component({
  selector: 'app-portfolio-creative-three',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    NavbarComponent,
    CreativePortfolioComponent,
    FooterFiveComponent
  ],
  templateUrl: './portfolio-creative-three.component.html',
  styleUrl: './portfolio-creative-three.component.scss'
})
export class PortfolioCreativeThreeComponent {

}
