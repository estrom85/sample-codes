/* 
 * File:   ViewModel.h
 * Author: mato
 *
 * Created on Nedeľa, 2013, december 8, 13:29
 */

#ifndef VIEWMODEL_H
#define	VIEWMODEL_H

#include <QtGui/QtGui>
#include <cmath>
#include <set>
#include "Package.h"
#include "PreviewDialog.h"

class ViewModel:public QAbstractTableModel {
    Q_OBJECT
public:
    ViewModel();
    virtual ~ViewModel();
    QString getPackagePath();
    
    int rowCount(const QModelIndex & parent = QModelIndex()) const;
    int columnCount(const QModelIndex&parent = QModelIndex()) const;
    QVariant data(const QModelIndex& index, int role) const;
    QVariant headerData ( int section, Qt::Orientation orientation, int role = Qt::DisplayRole ) const;
    
    void setSelectedItems(QModelIndexList indexes);
    
    bool packageLoaded();
    
public slots:
    void CreatePackage();
    void SavePackage();
    void LoadPackage();
    void AddFiles();
    void ExportFiles();
    void RemoveFiles();
    void EditKey();
    
signals:
    void PackageChanged(Package*);
    void FileListChanged();
private:
    Package* package;
    set<string> _selectedKeys;
    PreviewDialog* _prevDialog;
    
    QString formatFileLength(unsigned int length) const;
};

#endif	/* VIEWMODEL_H */

