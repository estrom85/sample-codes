/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * pokus2.java
 *
 * Created on 12.7.2012, 12:19:14
 */
package labels;

import java.awt.event.WindowAdapter;
import java.awt.event.WindowEvent;
import javax.swing.JDialog;
import javax.swing.JFrame;

/**
 *
 * @author martinm
 */
public class LabelSettingDialog extends JDialog {
    private CLabel label;
    /** Creates new form pokus2 */
    public LabelSettingDialog(JFrame frame, CLabel label) {
        super(frame,"Nastavenia štítku",true);
        this.setBounds(frame.getBounds().x+20, frame.getBounds().y+20, 250, 270);
        this.label=label;
        initComponents();
        buttonGroup1.add(this.after);
        buttonGroup1.add(this.before);
        buttonGroup1.add(this.under);
        
        this.addWindowListener(
                new WindowAdapter() {

                    @Override
                    public void windowClosed(WindowEvent e) {
                        closeWindow();
                    }
                   
                }
                );
    }
    
    private void closeWindow(){
        this.setVisible(false);
    }
    
    public void openDialog(){
        setComponents();
        this.setVisible(true);
    }

    private void setComponents(){
        this.company.setSelected(label.isSet(CLabel.COMPANY));
        this.name.setSelected(label.isSet(CLabel.NAME));
        this.building.setSelected(label.isSet(CLabel.BUILDING));
        this.country.setSelected(label.isSet(CLabel.COUNTRY));
        
        switch(label.getPostalPosition()){
            case CLabel.POSTAL_BEFORE:
                this.before.setSelected(true);
                break;
            case CLabel.POSTAL_AFTER:
                this.after.setSelected(true);
                break;
            case CLabel.POSTAL_UNDER:
                this.under.setSelected(true);
                break;
        }
    }
    /** This method is called from within the constructor to
     * initialize the form.
     * WARNING: Do NOT modify this code. The content of this method is
     * always regenerated by the Form Editor.
     */
    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        jSeparator1 = new javax.swing.JSeparator();
        buttonGroup1 = new javax.swing.ButtonGroup();
        jLabel1 = new javax.swing.JLabel();
        company = new javax.swing.JCheckBox();
        name = new javax.swing.JCheckBox();
        building = new javax.swing.JCheckBox();
        country = new javax.swing.JCheckBox();
        jSeparator2 = new javax.swing.JSeparator();
        jLabel2 = new javax.swing.JLabel();
        before = new javax.swing.JRadioButton();
        after = new javax.swing.JRadioButton();
        under = new javax.swing.JRadioButton();
        set = new javax.swing.JButton();
        jButton2 = new javax.swing.JButton();

        jLabel1.setHorizontalAlignment(javax.swing.SwingConstants.CENTER);
        jLabel1.setText("Nastavte polia, ktoré sa zobrazia na štítku");

        company.setText("Spoločnosť");

        name.setText("Meno, priezvisko");

        building.setText("Sídlo");

        country.setText("Krajina");

        jLabel2.setHorizontalAlignment(javax.swing.SwingConstants.CENTER);
        jLabel2.setText("Nastavte pozíciu PSČ");

        before.setText("vpredu");
        before.setToolTipText("");

        after.setText("vzadu");

        under.setText("dole");
        under.setToolTipText("");

        set.setText("Nastav");
        set.setToolTipText("");
        set.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                setActionPerformed(evt);
            }
        });

        jButton2.setText("Zrušiť");
        jButton2.setToolTipText("");
        jButton2.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                jButton2ActionPerformed(evt);
            }
        });

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(getContentPane());
        getContentPane().setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addGroup(layout.createSequentialGroup()
                        .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                            .addGroup(layout.createSequentialGroup()
                                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                                    .addComponent(company)
                                    .addComponent(building))
                                .addGap(18, 18, 18)
                                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                                    .addComponent(country)
                                    .addComponent(name)))
                            .addComponent(jLabel2, javax.swing.GroupLayout.DEFAULT_SIZE, 203, Short.MAX_VALUE)
                            .addGroup(layout.createSequentialGroup()
                                .addComponent(before)
                                .addGap(26, 26, 26)
                                .addComponent(after)
                                .addGap(18, 18, 18)
                                .addComponent(under))
                            .addGroup(layout.createSequentialGroup()
                                .addGap(22, 22, 22)
                                .addComponent(set)
                                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED, 33, Short.MAX_VALUE)
                                .addComponent(jButton2)
                                .addGap(20, 20, 20)))
                        .addContainerGap())
                    .addGroup(javax.swing.GroupLayout.Alignment.TRAILING, layout.createSequentialGroup()
                        .addComponent(jLabel1, javax.swing.GroupLayout.DEFAULT_SIZE, 203, Short.MAX_VALUE)
                        .addContainerGap())
                    .addGroup(javax.swing.GroupLayout.Alignment.TRAILING, layout.createSequentialGroup()
                        .addComponent(jSeparator2, javax.swing.GroupLayout.DEFAULT_SIZE, 203, Short.MAX_VALUE)
                        .addContainerGap())))
        );
        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addComponent(jLabel1)
                .addGap(18, 18, 18)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(company)
                    .addComponent(name))
                .addGap(18, 18, 18)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(building)
                    .addComponent(country))
                .addGap(21, 21, 21)
                .addComponent(jSeparator2, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(jLabel2)
                .addGap(18, 18, 18)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(before)
                    .addComponent(after)
                    .addComponent(under))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED, 34, Short.MAX_VALUE)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(set)
                    .addComponent(jButton2))
                .addGap(21, 21, 21))
        );
    }// </editor-fold>//GEN-END:initComponents

private void jButton2ActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButton2ActionPerformed
// TODO add your handling code here:
    this.closeWindow();
}//GEN-LAST:event_jButton2ActionPerformed

private void setActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_setActionPerformed
// TODO add your handling code here:
    int add=0;
    int remove=0;
    if(this.company.isSelected())
        add=add|CLabel.COMPANY;
    else
        remove=remove|CLabel.COMPANY;
    
    if(this.name.isSelected())
        add=add|CLabel.NAME;
    else
        remove=remove|CLabel.NAME;
    
    if(this.building.isSelected())
        add=add|CLabel.BUILDING;
    else
        remove=remove|CLabel.BUILDING;
    
    if(this.country.isSelected())
        add=add|CLabel.COUNTRY;
    else
        remove=remove|CLabel.COUNTRY;
    
    label.addFields(add);
    label.removeFields(remove);
    
    if(this.after.isSelected())
        label.setPostalPosition(CLabel.POSTAL_AFTER);
    else if(this.before.isSelected())
        label.setPostalPosition(CLabel.POSTAL_BEFORE);
    else
        label.setPostalPosition(CLabel.POSTAL_UNDER);
    this.closeWindow();
   
}//GEN-LAST:event_setActionPerformed

    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JRadioButton after;
    private javax.swing.JRadioButton before;
    private javax.swing.JCheckBox building;
    private javax.swing.ButtonGroup buttonGroup1;
    private javax.swing.JCheckBox company;
    private javax.swing.JCheckBox country;
    private javax.swing.JButton jButton2;
    private javax.swing.JLabel jLabel1;
    private javax.swing.JLabel jLabel2;
    private javax.swing.JSeparator jSeparator1;
    private javax.swing.JSeparator jSeparator2;
    private javax.swing.JCheckBox name;
    private javax.swing.JButton set;
    private javax.swing.JRadioButton under;
    // End of variables declaration//GEN-END:variables
}