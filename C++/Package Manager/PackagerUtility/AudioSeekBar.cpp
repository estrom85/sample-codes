/* 
 * File:   AudioSeekBar.cpp
 * Author: mato
 * 
 * Created on Piatok, 2013, december 13, 10:39
 */

#include <qt4/QtGui/qevent.h>

#include "AudioSeekBar.h"

AudioSeekBar::AudioSeekBar(QWidget* w) : QSlider(w) {
}

AudioSeekBar::~AudioSeekBar() {
}

void AudioSeekBar::mousePressEvent(QMouseEvent* ev){
    int mouse_pos;
    if(this->orientation()==Qt::Vertical){
        mouse_pos = ev->y();
    }else{
        mouse_pos = ev->x();
    }
    int range = this->maximum() - this->minimum();
    double point = (double)range /(double)this->width();
    int val = mouse_pos * point;
    this->setSliderPosition(val);
    emit sliderMoved(val);
    QSlider::mousePressEvent(ev);
}