import { Component, OnInit, Input } from '@angular/core';
import { NgIf } from '@angular/common';

@Component({
  selector: 'app-loadingIndicator',
  imports: [NgIf],
  standalone: true,
  templateUrl: './loadingIndicator.component.html',
  styleUrls: ['./loadingIndicator.component.scss']
})
export class LoadingIndicatorComponent implements OnInit {
  @Input() isLoading!: boolean;
  constructor() {}

  ngOnInit() {}
}