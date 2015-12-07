/* 
 * File:   PreviewDialog.h
 * Author: mato
 *
 * Created on Štvrtok, 2013, december 12, 9:05
 */

#ifndef _PREVIEWDIALOG_H
#define	_PREVIEWDIALOG_H

#include "ui_PreviewDialog.h"
#include <phonon/MediaObject>
#include <phonon/AudioOutput>
#include "Package.h"

class PreviewDialog : public QDialog {
    Q_OBJECT
public:
    PreviewDialog(QWidget* parent);
    virtual ~PreviewDialog();
    
    void setFileIndex(int);
private:
    Ui::PreviewDialog widget;
    
    int selectedIndex;
    
    Package* package;
    
    QPixmap* img;
    Phonon::MediaObject* audio;
    int fontId;
    
    void setFilePreview();
    
    void setImagePreview();
    void setAudioPreview();
    
public slots:
    void ChangePackage(Package*);
    void ShowDialog(const QModelIndex index);
    void ShowDialog();
    
private slots:
    void SetTicks(qint64);
    void stateChanged (Phonon::State);
    void SetAudioPosition(int);
    
protected:
    void closeEvent(QCloseEvent*);
        
private slots:
    void CloseDialog();
    void Prev();
    void Next();
    
private:
    void setView();
    void resetImageView(); 
    QString getTimeNumber(qint32);
    void setProgDisplay(int curTime);
    
};

#endif	/* _PREVIEWDIALOG_H */
