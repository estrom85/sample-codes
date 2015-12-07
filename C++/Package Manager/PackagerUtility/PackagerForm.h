/* 
 * File:   PackagerForm.h
 * Author: mato
 *
 * Created on Nedeľa, 2013, december 8, 12:54
 */

#ifndef _PACKAGERFORM_H
#define	_PACKAGERFORM_H

#include "ui_PackagerForm.h"
#include "ViewModel/ViewModel.h"

class PackagerForm : public QDialog {
    Q_OBJECT
public:
    PackagerForm();
    virtual ~PackagerForm();
private:
    Ui::PackagerForm widget;
    ViewModel* model;
    PreviewDialog* dialog;
    
public slots:
    void closeApp();
    void packageLoaded(Package*);
    void selectionChanged(const QItemSelection & selected, const QItemSelection & deselected);

private:
    void setButtons();
    void setPreviewDialog();
};

#endif	/* _PACKAGERFORM_H */
