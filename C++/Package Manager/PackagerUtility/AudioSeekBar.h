/* 
 * File:   AudioSeekBar.h
 * Author: mato
 *
 * Created on Piatok, 2013, december 13, 10:39
 */

#ifndef AUDIOSEEKBAR_H
#define	AUDIOSEEKBAR_H

#include <Qt/qslider.h>

class AudioSeekBar : public QSlider {
    Q_OBJECT
public:    
    AudioSeekBar(QWidget*);
    virtual ~AudioSeekBar();
    
protected:
    virtual void mousePressEvent(QMouseEvent* ev);

private:

};

#endif	/* AUDIOSEEKBAR_H */

